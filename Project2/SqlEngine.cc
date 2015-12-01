/**
 * Copyright (C) 2008 by The Regents of the University of California
 * Redistribution of this file is permitted under the terms of the GNU
 * Public License (GPL).
 *
 * @author Junghoo "John" Cho <cho AT cs.ucla.edu>
 * @date 3/24/2008
 */

#include <cstdio>
#include <cstring>
#include <cstdlib>
#include <iostream>
#include <fstream>
#include "Bruinbase.h"
#include "SqlEngine.h"
#include "BTreeIndex.h"

#include <climits>

using namespace std;

// external functions and variables for load file and sql command parsing 
extern FILE* sqlin;
int sqlparse(void);

RC checkAndPrint (SelCond::Comparator comp, int diff, int attr, int& count, int key, string value);


RC SqlEngine::run(FILE* commandline)
{
  fprintf(stdout, "Bruinbase> ");

  // set the command line input and start parsing user input
  sqlin = commandline;
  sqlparse();  // sqlparse() is defined in SqlParser.tab.c generated from
               // SqlParser.y by bison (bison is GNU equivalent of yacc)

  return 0;
}

RC SqlEngine::select(int attr, const string& table, const vector<SelCond>& cond)
{
  RecordFile rf;   // RecordFile containing the table
  RecordId   rid;  // record cursor for table scanning

  RC     rc;
  int    key;     
  string value;
  int    count;
  int    diff;

  int min = 0;
  int max = INT_MAX;
  int eql = -1;
  int neql = -1;
  int needRead = 0;

  int newMax = max;
  int newMin = min;

  if (attr == 2 || attr == 3)
    needRead = 1;

  /*  *  *  *  *  *  *  *  *  *  *  *  *  *
    CHECK IF RANGE/EQUALTY ON KEY EXISTS
  *  *  *  *  *  *  *  *  *  *  *  *  *  */
  for (unsigned i = 0; i < cond.size(); i++)
  {
    if (cond[i].attr == 1)
    {
      switch (cond[i].comp) {
        case SelCond::LT:
          newMax = atoi(cond[i].value) - 1;
          if (max > newMax)
            max = newMax;
          break;

        case SelCond::LE:
          newMax = atoi(cond[i].value);
          if (max > newMax)
            max = newMax;
          break;

        case SelCond::GT:
          newMin = atoi(cond[i].value) + 1;
          if (min < newMin)
            min = newMin;
          break;

        case SelCond::GE:
          newMin = atoi(cond[i].value);
          if (min < newMin)
            min = newMin;
          break;

        case SelCond::EQ:
          eql = atoi(cond[i].value);
          break; 
      }
    }
    else if (cond[i].attr == 2)
      needRead = 1;
  }

  // open the table file
  if ((rc = rf.open(table + ".tbl", 'r')) < 0) {
    fprintf(stderr, "Error: table %s does not exist\n", table.c_str());
    return rc;
  }

  /*  *  *  *  *  *  *  *  *  *  *  *
    REGULAR SEARCH OF INDEX SEARCH
  *  *  *  *  *  *  *  *  *  *  *  */
  BTreeIndex treeIndex;
  if (((min == 0) && (max == INT_MAX) && (eql == -1)) || // no range/equality
        ((rc = treeIndex.open(table + ".idx", 'r')) < 0)) // no index file
  {
    // IF NO RANGE/EQ OR INDEX FILE DNE = DO REGULAR SELECT
    // scan the table file from the beginning
    rid.pid = rid.sid = 0;
    count = 0;
    while (rid < rf.endRid()) {
      // read the tuple
      if ((rc = rf.read(rid, key, value)) < 0) {
        fprintf(stderr, "Error: while reading a tuple from table %s\n", table.c_str());
        goto exit_select;
      }

      // check the conditions on the tuple
      for (unsigned i = 0; i < cond.size(); i++) {
        // compute the difference between the tuple value and the condition value
        switch (cond[i].attr) {
        case 1:
        	diff = key - atoi(cond[i].value);
        	break;
        case 2:
        	diff = strcmp(value.c_str(), cond[i].value);
        	break;
        }

        // skip the tuple if any condition is not met
        switch (cond[i].comp) {
        case SelCond::EQ:
        	if (diff != 0) goto next_tuple;
        	break;
        case SelCond::NE:
        	if (diff == 0) goto next_tuple;
        	break;
        case SelCond::GT:
        	if (diff <= 0) goto next_tuple;
        	break;
        case SelCond::LT:
        	if (diff >= 0) goto next_tuple;
        	break;
        case SelCond::GE:
        	if (diff < 0) goto next_tuple;
        	break;
        case SelCond::LE:
        	if (diff > 0) goto next_tuple;
        	break;
        }
      }

      // the condition is met for the tuple. 
      // increase matching tuple counter
      count++;

      // print the tuple 
      switch (attr) {
      case 1:  // SELECT key
        fprintf(stdout, "%d\n", key);
        break;
      case 2:  // SELECT value
        fprintf(stdout, "%s\n", value.c_str());
        break;
      case 3:  // SELECT *
        fprintf(stdout, "%d '%s'\n", key, value.c_str());
        break;
      }

      // move to the next tuple
      next_tuple:
      ++rid;
    }
  }
  // ELSE INDEX FILE EXISTS && RANGE/EQAULITY QUERY
  else
  {
    count = 0;

    treeIndex.readInfo();

    fprintf(stderr, "HERE IS THE TREE HEIGHT: %d\n", treeIndex.getHeight());

    IndexCursor cursor;

    if (eql != -1) 
    {
      if ((rc = treeIndex.locate(eql, cursor)) < 0) {
        fprintf(stderr, "HERE IS THE ERROR %d\n", rc);
        goto exit_select;
      }

      if (needRead) {
        goto exit_select;
      }
      else {
        count++;
        fprintf(stdout, "%d\n", eql);
      }

      /*
      for (unsigned i = 0; i < cond.size(); i++) 
      {
        if (cond[i].attr == 2)
        {
          treeIndex.readLeafEntry(cursor.eid, key, rid, cursor);
          fprintf(stdout, "LOCATING CURSOR EID: %d, PID: %d RID: %d\n", cursor.eid, cursor.pid, rid.pid);

          if ((rc = rf.read(rid, key, value)) < 0) {
            fprintf(stderr, "Error: while reading a tuple from table %s\n", table.c_str());
            goto exit_select;
          }

          diff = strcmp(value.c_str(), cond[i].value);

          if (checkAndPrint(cond[i].comp, diff, attr, count, key, value) < 0)
            fprintf(stderr, "HERE IS THE ERROR in C&P");
        }
      }
      */
    }
  }


  // print matching tuple count if "select count(*)"
  if (attr == 4) {
    fprintf(stdout, "%d\n", count);
  }
  rc = 0;

  // close the table file and return
  exit_select:
  rf.close();
  return rc;
}

RC SqlEngine::load(const string& table, const string& loadfile, bool index)
{

  RecordFile rf;
  RecordId   rid;
  RC rc; 

  if ((rc = rf.open(table + ".tbl", 'w')) < 0) {
    fprintf(stderr, "Error with creating/opening table %s \n", table.c_str());
    return rc;
  }

  fstream loaded_file;
  loaded_file.open (loadfile.c_str(), fstream::in);

  if (!loaded_file) {
    fprintf(stderr, "Error opening file");
    return -1;
  }


  BTreeIndex treeIndex;
  if (index)
  {
    if (treeIndex.open(table + ".idx", 'w') < 0) {
      fprintf(stderr, "Error with creating/opening index %s \n", table.c_str());
      return rc;
    }

    treeIndex.readInfo();
  }

  string fileline;
  int key;
  string value;

  while(getline(loaded_file, fileline))
  {
    if (parseLoadLine(fileline, key, value) < 0)
    {
      fprintf(stderr, "Error parsing a file line\n");
      return -1;
    }

    if (rf.append(key, value, rid) < 0)
    {
      fprintf(stderr, "Error appending a tuple\n");
      return -1;
    }

    if (index)
    {
      //BTreeIndex treeIndex;
      treeIndex.insert(key, rid);
    }

  }

  fprintf(stdout, "FINAL TREE HEIGHT: %d\n", treeIndex.getHeight());

  loaded_file.close();

  return 0;
}

RC SqlEngine::parseLoadLine(const string& line, int& key, string& value)
{
    const char *s;
    char        c;
    string::size_type loc;
    
    // ignore beginning white spaces
    c = *(s = line.c_str());
    while (c == ' ' || c == '\t') { c = *++s; }

    // get the integer key value
    key = atoi(s);

    // look for comma
    s = strchr(s, ',');
    if (s == NULL) { return RC_INVALID_FILE_FORMAT; }

    // ignore white spaces
    do { c = *++s; } while (c == ' ' || c == '\t');
    
    // if there is nothing left, set the value to empty string
    if (c == 0) { 
        value.erase();
        return 0;
    }

    // is the value field delimited by ' or "?
    if (c == '\'' || c == '"') {
        s++;
    } else {
        c = '\n';
    }

    // get the value string
    value.assign(s);
    loc = value.find(c, 0);
    if (loc != string::npos) { value.erase(loc); }

    return 0;
}



RC checkAndPrint (SelCond::Comparator comp, int diff, int attr, int& count, int key, string value) {
  
  switch (comp) {
        case SelCond::EQ:
          if (diff != 0)
            return -1;
        case SelCond::NE:
          if (diff == 0)
            return -1;
        case SelCond::GT:
          if (diff <= 0)
            return -1;
        case SelCond::LT:
          if (diff >= 0)
            return -1;
        case SelCond::GE:
          if (diff < 0)
            return -1;
        case SelCond::LE:
          if (diff > 0) 
            return -1;
    }

  count++;

      // print the tuple 
  switch (attr) {
      case 1:  // SELECT key
        fprintf(stdout, "%d\n", key);
        break;
      case 2:  // SELECT value
        fprintf(stdout, "%s\n", value.c_str());
        break;
      case 3:  // SELECT *
        fprintf(stdout, "%d '%s'\n", key, value.c_str());
        break;
  }

  return 0;
}