# techhounds-cms2
Second attempt of creating a new Content Management System (CMS) for the TechHOUNDS Website. The old one was a pain to use so this is an attempt to create one tailored to our needs.
# How to install
Checkout the repo, then run `git submodule update --init` to get all of the dependencies.

Be sure to install the php zip extension and put jquery-1.11.3.js in the root directory.
Selinux must also allow php to write to the web dir or be disabled.
Any user that wants to edit files on the server (e.g. through ssh) must be part of the apache group.
