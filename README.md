# online_invoice
Generate online invoice

# Project setup Guide
# Step-1
composer install

# Step-2
In the root directory, you will find a file named .env.example, rename the given file name to .env and run the following command to generate the key.
php artisan key:generate

# Step-3
Run the migration
php artisan migrate

# Step-4
Add node modules
npm install
npm run dev


# Note : Laravel version 8.7 and require php version is >= 7.4.26
