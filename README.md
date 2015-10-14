# techhounds-cms2
Second attempt of creating a new Content Management System (CMS) for the TechHOUNDS Website. The old one was a pain to use so this is an attempt to create one tailored to our needs.
# How to install
Checkout the repo, then run `git submodule update --init` to get all of the dependencies.

Be sure to install the php zip extension and put jquery-1.11.3.js in the root directory.
Selinux must also allow php to write to the web dir or be disabled.

If following these steps is not possible on the server, complete these steps on a different computer and then upload it to the server.
#Initial setup
The page `<server url>/admin/init.php` must be visited first to initialize the data base.

`<server url>/admin/` can now be visited. The default login is username:`admin` password:`password`.

Additional documentation can be found within the main admin page.
