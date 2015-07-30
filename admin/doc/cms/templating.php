<?php require("/var/www/techhounds-cms2/admin/util.php");?>
<html>
  <head>
    <meta charset="utf-8">
    <title>Documentation</title>
    <link rel="stylesheet" type="text/css" href="/admin/css/style.css">
  </head>
  <body>
    <div class="body-container">
<h1>Templating</h1>
<h2>Introduction</h2>
<p>Designed to reduce the amount of code that needs to be written. Eliminates a lot of copypasta.<br>
Template path needs to be referenced from <code>/</code><br>
No template is also acceptable.</p>
<h2>Inserting Main Elements</h2>
<p><code>{{body}}</code><br>
<code>{{title}}</code><br>
<code>{{out_path}}</code><br>
<code>{{date}}</code><br>
Date is last edited date.</p>
<h2>Inserting Parameters</h2>
<p><code>{{param:&lt;name&gt;}}</code><br>
Parameters are set with the bottom text field of a dynamic file.</p>
<h3>Setting Parameters</h3>
<p><code>&lt;name&gt; &lt;value&gt;</code><br>
<code>&lt;name2&gt; &lt;value2&gt;</code><br>
<code>... ...</code><br></p>
<h2>Inserting Files</h2>
<p><code>{{file:&lt;file name&gt;}}</code><br>
File name needs to be referenced from <code>/</code></p>
<h3>Files Parameters</h3>
<p><code>{{file:&lt;file name&gt;,,&lt;param 1&gt;}}</code><br>
<code>{{file:&lt;file name&gt;,,&lt;param 1&gt;,,&lt;param 2&gt;,,&lt;...&gt;}}</code></p>
<h4>Using File Parameters</h4>
<p><code>{{var:0}}</code><br>
<code>{{var:1}}</code><br>
<code>...</code></p>
<h2>Setting Variables</h2>
<p><code>{{varset:&lt;name&gt;:&lt;value&gt;}}</code></p>
<h2>Clearing Variables</h2>
<p><code>{{varclear:&lt;name&gt;}}</code></p>
<h2>Inserting Variables</h2>
<p><code>{{var:&lt;name&gt;}}</code></p>
<h2>Eval Code</h2>
<p><code>{{eval:&lt;code&gt;}}</code><br>
The code within the eval statements are run during the generation of the templated file. This allows for loops and other more complex functions to be implemented.</p>
<p><a href="index.php">Back</a></p>
    </div>
  </body>
</html>
