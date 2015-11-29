Opened table with RecordFile::open
Opened file with fstream::open and used getline to get each line
Then performed parseLoadLine on each, getting key and value
And performed RecordFile::append of each key and value

Implemented BTLeafNode and BTNonLeafNode

Implemented BTreeIndex's functions
- Locate
	Iterate down to leaf node using locateChildPtr
	Read in corresponding leaf node, and locate the entry

- Insert
	recursively insert node
	check if leaf node, and split if there's overflow