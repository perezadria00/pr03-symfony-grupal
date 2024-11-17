# **Nurses Administration**

A nurse management system that allows managing personal information, authentication, and CRUD operations. This project is built using Symfony, with MySQL as the database and PHPUnit for automated testing.

---

## **Description**

The purpose of this project is to provide an efficient solution for nurse management in an administrative environment. The system includes:

- Creation, reading, updating, and deletion of nurse records.
- User authentication.
- Advanced search by name and surname.
- RESTful API to facilitate integration with other systems.

The project is designed to be scalable, modular, and easy to maintain, focusing on security and code quality.

---

## **Installation**

To set up and run the project locally, follow these steps:

1. Clone the repository and navigate to the project directory.

2. Install the required dependencies using Composer.

3. Configure the environment by creating a `.env.local` file with the necessary variables:
   - Set the application environment to development.
   - Add a secret key for the application.
   - Provide the database connection URL.

4. Create and configure the database:
   - Create the database.
   - Update the database schema.

5. Load sample data into the database using Doctrine Fixtures.

6. Start the Symfony development server to make the application accessible.

---

## **Usage**

### **Run the application**

1. Start the Symfony server to run the application.

2. Open your browser and navigate to the following address:
   - [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

### **API Endpoints**

#### **CRUD Operations for Nurses**

1. Retrieve a list of all nurses:
   - Endpoint: `/nurse/index`
   - Method: `GET`

2. Create a new nurse:
   - Endpoint: `/nurse/new`
   - Method: `POST`
   - Request Body (JSON):
     - User: The username of the nurse.
     - Password: The password for authentication.
     - Name: The first name of the nurse.
     - Surname: The last name of the nurse.

3. Retrieve a nurse by their ID:
   - Endpoint: `/nurse/{id}`
   - Method: `GET`

4. Update a nurse's information:
   - Endpoint: `/nurse/{id}/edit`
   - Method: `PUT`
   - Request Body (JSON):
     - Include any fields to update, such as `name`.

5. Delete a nurse by their ID:
   - Endpoint: `/nurse/{id}`
   - Method: `DELETE`

---

### **Additional Features**

1. Search nurses by their name and surname:
   - Endpoint: `/nurse/name/{name}/{surname}`
   - Method: `GET`

2. Authenticate a nurse:
   - Endpoint: `/nurse/login/{username}/{password}`
   - Method: `GET`

---

### **Tests**

1. Execute unit tests to verify the application’s functionality.

2. Ensure all tests pass before deploying the application to production.

---

## **Contributing**

Contributions are welcome! If you'd like to contribute, please open an issue or submit a pull request to discuss the changes you'd like to make.

---



