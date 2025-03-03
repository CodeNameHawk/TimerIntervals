# Timer Intervals Manager

A program for managing and merging overlapping time intervals.

## Features

* Input time intervals in HH:MM in 24H format
* Add multiple intervals via form interface
* View all saved intervals
* View automatically merged intervals
* Delete intervals with a click
* Export merged intervals to file

## Usage

1. Enter interval times:
   * Start time (HH:MM)
   * End time (HH:MM)

2. Interface sections:
   * Left column: Saved intervals
   * Middle column: Merged intervals
   * Right column: Input form

3. Operations:
   * Click intervals to select/delete
   * Use 'Save to file' to export results

## Example

```
Input: 09:00-10:30, 10:00-11:00
Result: 09:00-11:00 (merged)
```