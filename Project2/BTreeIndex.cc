/*
 * Copyright (C) 2008 by The Regents of the University of California
 * Redistribution of this file is permitted under the terms of the GNU
 * Public License (GPL).
 *
 * @author Junghoo "John" Cho <cho AT cs.ucla.edu>
 * @date 3/24/2008
 */
 
#include "BTreeIndex.h"
#include "BTreeNode.h"

using namespace std;

/*
 * BTreeIndex constructor
 */
BTreeIndex::BTreeIndex()
{
    rootPid = -1;
}

// Our helper functions to find stored variable information
RC BTreeIndex::readInfo() {
	if (pf.read(0, buffer) < 0)
		return RC_FILE_READ_FAILED;

	PageId* getroot = (PageId*) buffer;
	rootPid = *getroot;

	int* getheight = (int*) (buffer + sizeof(PageId));
	treeHeight = *getheight;

	return 0;
}

RC BTreeIndex::writeInfo() {
	PageId* getroot = (PageId*) buffer;
	*getroot = rootPid;

	int* getheight = (int*) (buffer + sizeof(PageId));
	*getheight = treeHeight;

	return pf.write(0, buffer);
}

/*
 * Open the index file in read or write mode.
 * Under 'w' mode, the index file should be created if it does not exist.
 * @param indexname[IN] the name of the index file
 * @param mode[IN] 'r' for read, 'w' for write
 * @return error code. 0 if no error
 */
RC BTreeIndex::open(const string& indexname, char mode)
{
	return pf.open(indexname, mode);
}

/*
 * Close the index file.
 * @return error code. 0 if no error
 */
RC BTreeIndex::close()
{
    return pf.close();
}

RC BTreeIndex::recInsert(int key, const RecordId& rid, PageId pid, int& midKey,
                   int currheight, PageId& leftChild, PageId& rightChild)
{
    // Check if the node is a leafNode
    if (treeHeight == currheight) {
        BTLeafNode newleaf;
        if(newleaf.read(pid, pf) < 0)
        	return RC_FILE_READ_FAILED;

        // Check if the insert will cause overflow
        if (newleaf.getKeyCount() < BTLeafNode::max_key_count) {
            newleaf.insert(key, rid);
            newleaf.write(pid, pf);
        } else {
            BTLeafNode sibling;
            newleaf.insertAndSplit(key, rid, sibling, midKey);

            PageId sibPid = pf.endPid();
            sibling.write(sibPid, pf);

            newleaf.setNextNodePtr(sibPid);

            leftChild = pid;
            rightChild = sibPid;
        }
        return 0;
    } 

    else {
        // Find the proper pointer to child
        BTNonLeafNode newNonleaf;
        newNonleaf.read(pid, pf);
        newNonleaf.locateChildPtr(key, pid);

        currheight++;

        recInsert(key, rid, pid, midKey, currheight, leftChild, rightChild);
        // Check if insert causes overflow in a child node
        if (midKey == 0) {
            return 0;
        }
        if (newNonleaf.getKeyCount() < BTLeafNode::max_key_count) {
            newNonleaf.insert(midKey, rightChild);
            midKey = 0;
        } else {
            BTNonLeafNode sib;
            PageId sibPid = pf.endPid();
            
            newNonleaf.insertAndSplit(key, rightChild, sib, midKey);
            sib.write(sibPid, pf);

            leftChild = pid;
            rightChild = sibPid;
        }
    }
    currheight++;
    return 0;
}

/*
 * Insert (key, RecordId) pair to the index.
 * @param key[IN] the key for the value inserted into the index
 * @param rid[IN] the RecordId for the record being inserted into the index
 * @return error code. 0 if no error
 */
RC BTreeIndex::insert(int key, const RecordId& rid)
{
	if (rootPid < 1) {

		rootPid = pf.endPid();
        BTLeafNode newRoot;
        newRoot.write(pf.endPid(), pf);
        newRoot.insert(key, rid);
        //treeHeight++;
    } 

    else {
        int currheight = 0;
        PageId leftChild = -1;
        PageId rightChild = -1;
        int midKey = 0;
        recInsert(key, rid, rootPid, midKey, currheight, leftChild, rightChild);



        // Check if theres a split at root node
        if (midKey != 0) {
            BTNonLeafNode newNonRoot;
            rootPid = pf.endPid();
            
            newNonRoot.initializeRoot(leftChild, midKey, rightChild);
            newNonRoot.write(rootPid, pf);
            treeHeight++;
        }

        //treeHeight++;
    }

    return 0;

/*
	if (treeHeight == 0){
		BTLeafNode newRoot;
		newRoot.insert(key, rid);
		rootPid = pf.endPid();
		newRoot.write(rootPid,pf);
		treeHeight++;
		return 0;
	}

	BTLeafNode newLeaf;
	IndexCursor pointer;
	this->locate(key, pointer);
	newLeaf.read(pointer.Pid, pf);

		if (newLeaf.getkeyCount() < 84){
			newLeaf.insert(key, rid);
			newLeaf.write(pointer.Pid, pf);
		} else {
			BTLeafNode sibling;
			int siblingKey;
			newLeaf.insertAndSplit(key, rid, sibling, siblingKey);
			newleaf.write(pointer.pid, pf);
			sibling.write(pf.endPid(), pf);
		}
		return 0;

		BTNonLeafNode newNonleaf;
		newNonleaf.read(pid, pf);
        newNonleaf.locateChildPtr(key, pid);
        recInsert(key, rid, pid, midKey, height, childLeft, childRight);
        */
}

/**
 * Run the standard B+Tree key search algorithm and identify the
 * leaf node where searchKey may exist. If an index entry with
 * searchKey exists in the leaf node, set IndexCursor to its location
 * (i.e., IndexCursor.pid = PageId of the leaf node, and
 * IndexCursor.eid = the searchKey index entry number.) and return 0.
 * If not, set IndexCursor.pid = PageId of the leaf node and
 * IndexCursor.eid = the index entry immediately after the largest
 * index key that is smaller than searchKey, and return the error
 * code RC_NO_SUCH_RECORD.
 * Using the returned "IndexCursor", you will have to call readForward()
 * to retrieve the actual (key, rid) pair from the index.
 * @param key[IN] the key to find
 * @param cursor[OUT] the cursor pointing to the index entry with
 *                    searchKey or immediately behind the largest key
 *                    smaller than searchKey.
 * @return 0 if searchKey is found. Othewise an error code
 */
RC BTreeIndex::locate(int searchKey, IndexCursor& cursor)
{
	// pid 0 is saved for variable storage. rootPid cannot be 0
	if (rootPid < 1)
		return RC_NO_SUCH_RECORD;

	cursor.pid = rootPid;

	// at root
	int currHeight = 0;

	while (currHeight < treeHeight)
	{
		BTNonLeafNode nonLeaf;
		if (nonLeaf.read(cursor.pid, pf) < 0)
			return RC_FILE_READ_FAILED;

		RC childPtr = nonLeaf.locateChildPtr(searchKey, cursor.pid);
		if (childPtr < 0)
			return RC_NO_SUCH_RECORD;

		currHeight++;
	}

	BTLeafNode leafNode;

	if (leafNode.read(cursor.pid, pf) < 0)
		return RC_FILE_READ_FAILED;

	if (leafNode.locate(searchKey, cursor.eid))
		return RC_NO_SUCH_RECORD;


    return 0;
}

/*
 * Read the (key, rid) pair at the location specified by the index cursor,
 * and move foward the cursor to the next entry.
 * @param cursor[IN/OUT] the cursor pointing to an leaf-node index entry in the b+tree
 * @param key[OUT] the key stored at the index cursor location.
 * @param rid[OUT] the RecordId stored at the index cursor location.
 * @return error code. 0 if no error
 */
RC BTreeIndex::readForward(IndexCursor& cursor, int& key, RecordId& rid)
{
	BTLeafNode readNode;

	if (readNode.read(cursor.pid, pf) < 0)
		return RC_FILE_READ_FAILED;

	if (readNode.readEntry(cursor.eid, key, rid) < 0)
		return RC_NO_SUCH_RECORD;

	cursor.eid++;

    return 0;
}
