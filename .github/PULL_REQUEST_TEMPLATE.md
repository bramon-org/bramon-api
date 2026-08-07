---
name: Auto-create station from capture analyze data
about: Create stations automatically when importing/processsing A.XML captures
labels: enhancement
assignees: mrprompt
---

This PR implements automatic creation of Station records when processing UFO analyze files (A.XML) during import or upload. It addresses issue #12.

What changed
- The UfoDriver now attempts to find a Station from parsed analyze metadata and will create one if CAPTURE_AUTO_CREATE_USER_ID is configured.
- The import:captures command will also attempt to auto-create stations using the driver when needed.
- Adds a functional test verifying station auto-creation from an A.XML file.
- Documents the CAPTURE_AUTO_CREATE_USER_ID environment variable.

Configuration
- To enable automatic station creation, set CAPTURE_AUTO_CREATE_USER_ID to a valid user UUID that will be set as the station owner.

Behavior notes
- Newly created stations are created with visible=false by default to avoid exposing incomplete stations in listings until they are reviewed.
- Automatic creation is only performed when the env var is set.

Closes: #12
