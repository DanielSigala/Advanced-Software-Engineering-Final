# Advanced-Software-Engineering-Final

## About

This repository includes the code for my Spring 2024 Advanced Software Engineering Final at The University of Texas at San Antonio. It is an inventory management site consisting of a site that handles the database and API, as well as an external site that uses API calls to add, remove, and edit inventory.

## Table of Contents

- [Project Structure](#project-structure)
- [Installation](#installation)
- [Usage](#usage)
- [API Endpoints](#api-endpoints)
  - [Modify Equipment API](#modify-equipment-api)
  - [Search Equipment API](#search-equipment-api)
- [Contributing](#contributing)

## Project Structure


# Advanced-Software-Engineering-Final

## About

This repository includes the code for my Spring 2024 Advanced Software Engineering Final at The University of Texas at San Antonio. It is an inventory management site consisting of a site that handles the database and API, as well as an external site that uses API calls to add, remove, and edit inventory.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [API Endpoints](#api-endpoints)
  - [Modify Equipment API](#modify-equipment-api)
  - [Search Equipment API](#search-equipment-api)
- [Contributing](#contributing)
- [License](#license)


## Installation

1. Clone the repository:

    ```bash
    git clone https://github.com/DanielSigala/Advanced-Software-Engineering-Final.git
    ```

2. Navigate to the project directory:

    ```bash
    cd Advanced-Software-Engineering-Final
    ```

3. Set up the database (not yet Implemented, simply used to view not execute):

    - Import the `initialize_db.sql` file into your MySQL database.

    ```sql
    source db/initialize_db.sql;
    ```

4. Configure database connection:

    - Update the `db/db_connect.php` file with your database connection details.

## Usage

1. Start the server to host the API and database handling site.

2. Open the external site in a web browser:

    - Navigate to `external-site/index.html`.

3. Use the external site to interact with the inventory management system through the provided UI.

## API Endpoints

### Modify Equipment API

**Endpoint:** `api/modify_equipment.php`

**Method:** `POST`

**Parameters:**
- `serial_number` (required): Original serial number of the equipment.
- `new_device_type` (optional): New device type.
- `new_manufacturer_name` (optional): New manufacturer name.
- `new_serial_number` (optional): New serial number.
- `new_status` (optional): New status ('active' or 'inactive').

**Responses:**
- **Success:** 
    ```json
    {
        "Status": "SUCCESS",
        "MSG": "Equipment details modified successfully. Updated fields: [fields].",
        "Action": "none"
    }
    ```
- **Error:** 
    - If original serial number is missing:
        ```json
        {
            "Status": "ERROR",
            "MSG": "Original serial number is required.",
            "Action": "Input_serial_number"
        }
        ```
    - If original serial number exceeds 67 characters:
        ```json
        {
            "Status": "ERROR",
            "MSG": "Original serial number exceeds 67 characters.",
            "Action": "Verify_serial_number_length"
        }
        ```
    - If original serial number does not exist:
        ```json
        {
            "Status": "ERROR",
            "MSG": "Original serial number does not exist.",
            "Action": "Verify_serial_number_existence"
        }
        ```
    - And more based on different validation checks.

### Search Equipment API

**Endpoint:** `api/search_equipment.php`

**Method:** `GET`

**Parameters:**
- `searchType` (required): Type of search ('device', 'manufacturer', 'serial_number').
- `searchQuery` (required): Query string to search for.
- `status` (optional): Filter results by status ('active', 'inactive', 'all'). Default is 'active'.

**Responses:**
- **Success:** 
    ```json
    {
        "Status": "SUCCESS",
        "Data": [Array of found equipment details],
        "Action": "none"
    }
    ```
- **Error:** 
    - If search query is empty:
        ```json
        {
            "Status": "ERROR",
            "MSG": "Search query cannot be empty.",
            "Action": "None"
        }
        ```
    - If search query type is invalid:
        ```json
        {
            "Status": "ERROR",
            "MSG": "Invalid search query type.",
            "Action": "validate_search_query_type"
        }
        ```
    - If status parameter is invalid:
        ```json
        {
            "Status": "ERROR",
            "MSG": "Invalid status parameter.",
            "Action": "None"
        }
        ```
    - And more based on different validation checks.

## Contributing

Contributions are welcome! Please open an issue or submit a pull request for any changes or improvements.


