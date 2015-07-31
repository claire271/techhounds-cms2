<?php require("/var/www/techhounds-cms2/admin/util.php");?>
<html>
  <head>
    <meta charset="utf-8">
    <title>Documentation</title>
    <link rel="stylesheet" type="text/css" href="/admin/css/style.css">
  </head>
  <body>
    <div class="body-container">
<h1>Backups and Restores</h1>
<h2>Introduction</h2>
<p>This CMS supports the ability to do selective backups.
This allows for the syncrhonizing of two instances of this CMS.</p>
<h2>Generating File Hashes</h2>
<p>This button on the main page takes every file in the CMS and create a hash of it if it is smaller than 16Mb.
It outputs an archive of all the files.</p>
<h2>Generating Backups</h2>
<p>This button creates a backup of all files in the CMS that are smaller than 16Mb.
If an archive with hashes is uploaded to this function, only files that have changed and are newer are included in the backup archive.</p>
<h2>Restoring Backups</h2>
<p>This button restores all the files from the archive.</p>
<h2>Backing up a single server</h2>
<p>To back up a single server, simply use the generate backups button. To restore that backup later, upload the backup archive, and use the restore backup button.<br>
Remember that files over 16Mb are not backed up.</p>
<h2>Synchronizing between two servers</h2>
<ol>
<li>On the target server, generate the hash archive.</li>
<li>On the origin server, upload that hash archive and create a backup.</li>
<li>On the target server, upload the backup archive and restore the backup.</li>
</ol>
<p>The target server should now contain the changes made on the origin server.<br>
Remember that files over 16Mb will not be transfered.</p>
<p><a href="index.php">Back</a></p>
    </div>
  </body>
</html>
