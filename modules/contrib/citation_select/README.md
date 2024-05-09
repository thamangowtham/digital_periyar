# Citation Select #
Adds a block that allows users to select and view citations of a node object from a list of citation styles. Uses Citation Style Language (CSL) provided by the Bibcite module.

## Setup and Usage ##
### Requirements ###
- Token module
- Bibcite module

### Installation ###
Download module using Composer, and enable with Drush.

### Configuration ###
#### Block ####
1. Place the "Citation Select Block" as a Drupal block (for example, at Structure › Block Layout).
2. Select the content types that the block should appear in under Configure Block

#### Mapping ####
1. Navigate to Configuration › Bibliography & Citation › Citation Select CSL Mapping.
2. Select node fields to map citation fields from
    1. Mappings can be set for the "type" of citation (e.g. book, document). For example, if your system uses the term `Paged Content` to identify books, the Name of that term (e.g. "Paged Content") can be mapped to the CSL-recognized term `book`. If there is no [valid type](https://docs.citationstyles.org/en/stable/specification.html?#appendix-iii-types) or the field cannot be found, then the type is set to `document`
    2. Mappings can also be set for Typed Relation fields (see [Controlled Access Terms](https://github.com/Islandora/controlled_access_terms) module). For example, if the machine name of a relation is `relators:aut`, that can be mapped to `author` so that the correct relations can be extracted for the author field in CSL. Note that the typed relation field must also be set in the CSL field mapping for the "author" CSL field.

#### Styles ####
1. Upload CSL styles using bibcite to add more citation options.

### Usage ###
1. Place the Citation Select block to appear on desired node pages.
2. Configure the Citation Select CSL Mapping to map node fields to CSL fields.
3. Install some CSL styles.
4. Use the Citation Select block on the node's page to choose a citation style. The formatted citation will appear below the block and can be copied with the "Copy citation" button.
