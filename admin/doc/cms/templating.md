# Templating

## Introduction
Designed to reduce the amount of code that needs to be written. Eliminates a lot of copypasta.<br>
Template path needs to be referenced from `/`<br>
No template is also acceptable.

## Inserting Main Elements
`{{body}}`<br>
`{{title}}`<br>
`{{out_path}}`<br>
`{{date}}`<br>
Date is last edited date.

## Inserting Parameters
`{{param:<name>}}`<br>
Parameters are set with the bottom text field of a dynamic file.

### Setting Parameters
`<name> <value>`<br>
`<name2> <value2>`<br>
`... ...`<br>

## Inserting Files
`{{file:<file name>}}`<br>
File name needs to be referenced from `/`

### Files Parameters
`{{file:<file name>,,<param 1>}}`<br>
`{{file:<file name>,,<param 1>,,<param 2>,,<...>}}`

#### Using File Parameters
`{{var:0}}`<br>
`{{var:1}}`<br>
`...`

## Setting Variables
`{{varset:<name>:<value>}}`

## Clearing Variables
`{{varclear:<name>}}`

## Inserting Variables
`{{var:<name>}}`

## Eval Code
`{{eval:<code>}}`<br>
The code within the eval statements are run during the generation of the templated file. This allows for loops and other more complex functions to be implemented.

[Back](index.php)