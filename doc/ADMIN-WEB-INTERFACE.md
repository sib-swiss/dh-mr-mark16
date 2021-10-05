# Admin web interface manual
This manual describes the process to update the contents of the Manuscript room.
For each manuscript, one or several folios are presented. For each folio the contents are:
1. An image: the picture (`JPG`, `PNG`...) of the folio.
2. A metadata file: an `XML`  `RDF` document containing metadata about the folio, its manuscript, and an abstract. Metadata files are readed from https://www.nakala.fr/ (where all the manuscript's material are stored).
3. The transcript(s): one `HTML` file containing the transcript of the folio in its original language, and eventually another for the english translation are stored in Nakala.
> Partners: one or several partner logos and its URL should be added.

## How to Add Manuscript
1. Click "Add Manuscript" will ask for Nakala URL of the manuscript to be added, for example: https://nakala.fr/10.34847/nkl.6f83096n
2. Click "Parse" button will read from Nakala URL and show a preview of meta content related to the Manuscript.
3. Confirm will add Manuscript to the Room, next steps will be associate Images (and copyright text if needed) to each folios and annd Partners (image and URL) if needed


## How to Edit Manuscript


Open [admin](/admin) to reach the DASHBOARD where all Manuscipts a re listed.
For each one there are **5 buttons**:

- **Edit**  Go in edit mode, displaying 5 tabs

     1. **Presentation**  

        - Display main metadata of the Manuscipt, modifiable only from Nakala.
          
     
     2. **Images**  
     
        - Display Manuscript Folio's images, for each one you can click over it and edit
         * Choose a local file to upload (JPG): requiremens: max size, resampled..
         * Copyright Text: text that will be overimpressed in the bottom of the image
           
     
     3. **Partners**
        Display Manuscript's partners, you can:
     
        * Edit: click on existing one to edit:
          - Upload image
          - Insert URL
        * Add new partner:
          - Image: choose local file to upload
          - URL: insert partner url that will be one clicking over the image
            
     4. **HTML**
     Display all HTML contents related to the Manuscript Folios, they are translations and the Diplomatic text. Cannot be modified, all HTML reside in Nakala and can be edit onlty from there.
     5. **XML**
         Display all XML contents related to the Manuscript Folios, they are TEI contrent anche can be modfied only from Nakala


-  **View**
Go in view mode, same tabs as edit are displayed but without the ability to edit

-  **HTML** Go directly in HTML tab in View mode

-  **XML** Go directly in XML tab in View mode

-  **Sync from Nakala**: Update local Manuscript's contents (metadata, HTML, XML) from Nakala, not images or partners.

-  **Switch status Publihed / Not Published** This status manage the visisbility of the Manuscripts in the Manuscripts Room.

## Clear Cache:
Cache is feature to make Manuscript Room faster, avoiding to rebuild in real time at each browser request the response (images and HTML template etc) and istead of it serve in response static files prebuilt.
Sometimes after some changes Cache needs to be Clear in order to be rebuilt with the updates.
This buttom will Clear Cache

## Database Button
Manuscript Room store information inside a Sqlite database. This Button will open webinterface to manage database for Developers. To run some Query or modify Structure after some code modifications.


## Help Button
Help button contains some usefull links, for example the Official page of Fat Free Framework, the PHP framework in this Project.


## Light/Dark

Switch browser theme of the admin interface between "Light" and "Dark"