# Pinecone PHP Client

This is a PHP client implementation for handling all of the Pinecone endpoints. This library was created because there was no existing PHP client for Pinecone vector database.

## Installation

To install the Pinecone PHP Client, you can use Composer. Run the following command:

```
composer require pinecone-io/pinecone-php-client
```

## Usage

### Initialization

To use the Pinecone PHP Client, you need to first initialize it with your API key and Pinecone endpoint. You can do this by creating a new instance of the Pinecone client:

```php
use Pinecone\PineconeClient;

$client = new PineconeClient('your_api_key', 'https://your.pinecone.endpoint.com');
```

### Creating an Index

To create a new index, you can use the `createIndex` method:

```php
$client->createIndex('index_name');
```

### Inserting Vectors

To insert a vector into an index, you can use the `insert` method:

```php
$client->insert('index_name', $vector);
```

### Querying Vectors

To query a vector from an index, you can use the `query` method:

```php
$client->query('index_name', $vector);
```

### Deleting Vectors

To delete a vector from an index, you can use the `delete` method:

```php
$client->delete('index_name', $vector_id);
```

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](https://choosealicense.com/licenses/mit/)