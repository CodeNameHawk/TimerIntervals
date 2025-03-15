# Timer Intervals Manager

A program for managing and merging overlapping time intervals.

## Features

* Input time intervals in HH:MM in 24H format
* Add multiple intervals via form interface
* View all saved intervals
* View automatically merged intervals
* Delete intervals with a click
* Export merged intervals to file

## Installation and Setup

### English
To use this application, you need to either:

1. Run locally:
   * Install PHP on your computer
   * Navigate to TimerIntervals folder
   * Open Terminal/Command Prompt
   * Start PHP server with command: `php -S localhost:9000`
   * Open in browser: http://localhost:9000/index.php

2. Or deploy on web server:
   * Place TimerIntervals folder on any web server with PHP support

### Russian
Чтобы это использовать, надо:

1. Для локального запуска:
   * Установить PHP
   * Перейти в папку TimerIntervals
   * Запустить Terminal, убедитесь, что вы находитесь в папке TimerIntervals.
   * Запустить сервер командой: `php -S localhost:9000`
   * Открыть в браузере http://localhost:9000/index.php

2. Или разместить папку TimerIntervals на веб сервере с поддержкой PHP

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

Input: 09:00-10:30, 10:00-11:00  
Result: 09:00-11:00 (merged)
