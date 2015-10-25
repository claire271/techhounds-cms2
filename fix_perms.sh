#!/bin/sh
find . -type d -exec chmod 755 {} +
find . -type f ! -name '*.sh' -exec chmod 644 {} +
find . -type f -name '*.sh' -exec chmod 755 {} +
find . -exec chown apache:apache {} +
