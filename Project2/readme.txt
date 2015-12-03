Chul Hee Woo: chwl3@ucla.edu
Brendan Sio: Bsio@ucla.edu

WE USED THE 2 DAY GRACE PERIOD FOR PART 2D

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

Part 2D:
	My BTreeIndex saves PID 0 for storing information about the general
	tree and hence saw one extra read (or few for xlarge which reinitailizes)
	the root node few times, which was deemed okay by the comments in the test
	However, I see that for count(*) cases, my implementation actually reads
	fewer pages (19 instead of 21 in large for example) while getting the
	correct result. 