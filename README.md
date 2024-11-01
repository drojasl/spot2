# Steps to Run the Project Locally

1. **Install dependencies**
    
    ```bash
    composer install
    ```
    ```bash
    npm install
    ```

2. **Start the Laravel Server**

    ```bash
    php artisan serve
    ```

3. **Start React**

    ```bash
    npm run dev
    ```

4. **Configure Environment Variables**

    - Copy the `.env.example` file and rename it to `.env`:

      ```bash
      cp .env.example .env
      ```

5. **Generate the Application Key**

    ```bash
    php artisan key:generate
    ```

6. **Run Migrations**

    ```bash
    php artisan migrate
    ```