# Backups and Restores

## Introduction
This CMS supports the ability to do selective backups.
This allows for the syncrhonizing of two instances of this CMS.

## Generating File Hashes
This button on the main page takes every file in the CMS and create a hash of it if it is smaller than 16Mb.
It outputs an archive of all the files.

## Generating Backups
This button creates a backup of all files in the CMS that are smaller than 16Mb.
If an archive with hashes is uploaded to this function, only files that have changed and are newer are included in the backup archive.

## Restoring Backups
This button restores all the files from the archive.

## Backing up a single server
To back up a single server, simply use the generate backups button. To restore that backup later, upload the backup archive, and use the restore backup button.<br>
Remember that files over 16Mb are not backed up.

## Synchronizing between two servers
1. On the target server, generate the hash archive.
2. On the origin server, upload that hash archive and create a backup.
3. On the target server, upload the backup archive and restore the backup.

The target server should now contain the changes made on the origin server.<br>
Remember that files over 16Mb will not be transfered.

[Back](index.php)