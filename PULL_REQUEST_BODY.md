---
name: feat: auto-create Station from capture analyze data (issue #12)
about: Create stations automatically when importing/processsing A.XML captures
labels: enhancement
assignees: mrprompt
---

When processing UFO analyze files (A.XML), if the station referenced does not exist we now auto-create the Station using the metadata parsed from the analyze XML and associate the Capture to it. This behavior is enabled when the environment variable CAPTURE_AUTO_CREATE_USER_ID is set (provides user_id to assign to newly created stations).

Files changed:
- app/Drivers/UfoDriver.php
- app/Console/Commands/ImportCapturesCommand.php
- tests/Functional/Drivers/UfoDriverAutoCreateTest.php
- docs/README.md

Closes: #12
