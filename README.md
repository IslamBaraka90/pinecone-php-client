# Pinecone PHP Client

This is a PHP client implementation for handling all of the Pinecone endpoints. This library was created because there was no existing PHP client for Pinecone vector database.
# Pinecone Vector Database

Pinecone is a vector database designed for high-performance similarity search and other similarity-related tasks. It provides a simple API for inserting and querying high-dimensional vectors, making it easy to build intelligent search applications that can handle large amounts of data.

Pinecone is used by OpenAI applications as part of its long-term memory architecture. By storing high-dimensional vectors in Pinecone, OpenAI applications can retrieve them quickly and efficiently when needed, allowing it to build complex models that can learn from large amounts of data and make more accurate predictions.

Pinecone's vector database is highly scalable and can handle millions or even billions of vectors with ease. It's designed to work with a variety of machine learning frameworks and libraries, making it easy to integrate into your existing workflow.

One of the key benefits of using Pinecone is its ability to perform fast, accurate similarity searches. This makes it ideal for applications such as image search, product recommendations, and personalized content delivery. By using Pinecone, you can build intelligent search applications that can handle large amounts of data and provide accurate results in real-time.

Overall, Pinecone is a powerful tool for anyone who needs to work with high-dimensional vectors and wants to build intelligent search applications that can handle large amounts of data. Whether you're building a recommendation engine, an image search application, or a personalized content delivery system, Pinecone can help you achieve your goals quickly and easily.

**Important Note:**

This library isn't restricted to only OpenAI embedding and can be used with any other embedding.

Make sure to check the dimensions of your embedding before using it with this library.
## Installation

To install the Pinecone PHP Client, you can use Composer. Run the following command:

```
composer require islambaraka90/pinecone-php-client
```

## Usage

### Initialization

To use the Pinecone PHP Client, you need to first initialize it with your API key and Pinecone endpoint. You can do this by creating a new instance of the Pinecone client:

```php
use IslamBaraka90\PineconeClient;

$this->api_key =  $_ENV['PINCONE_KEY'] ?: '';
$this->environment = $_ENV['PINCONE_ENV'] ?: 'us-east1-gcp';
$this->client = new PineconePhpClient( $this->api_key ,  $this->environment );
```

### Listing Indexes

To list all indexes available in your Pinecone instance, you can use the `listIndexes` method:

```php
$response = $this->client->listIndexes();
```

### Creating an Index

To create a new index, you can use the `createIndex` method:

```php
$name = 'my-index';
$dimension = 1536;
$result = $this->client->createIndex($name, $dimension);
```

### Deleting an Index

To delete an index, you can use the `deleteIndex` method:

```php
$response = $this->client->deleteIndex('my-index');
```

### Describing an Index

To get information about an index, you can use the `describeIndex` method:

```php
$this->client->describeIndex('my-index');
```

### Working with an Index

To work with a specific index, you can use the `Index` method to get an instance of the `Index` interface. Here are some examples of methods available on the `Index` interface:

#### Describe Index Stats

To get statistics about an index, you can use the `describeIndexStats` method:

```php
$index = $this->client->Index('my-index');
$state = $index->describeIndexStats();
```

#### Upsert Vectors

To insert or update vectors in an index, you can use the `upsert` method:

```php
$index = $this->client->Index('my-index');
$state = $index->describeIndexStats();

// Upsert 20 vectors
$vectorsFile = __DIR__ . '/vectors_objects.json';
$vectors = json_decode(file_get_contents($vectorsFile), true);
$project_id = 'project-0000'.rand(1,99);
$response = $index->upsert($project_id,$vectors['vectors']);
```

#### Query Vectors

To query vectors from an index, you can use the `query` method:

```php
$index = $this->client->Index('my-index');
$state = $index->describeIndexStats();

// Query vectors
$vectorsFile = __DIR__ . '/query.json';
$vectors = json_decode(file_get_contents($vectorsFile), true);
$response = $index->query('project-000001',$vectors, [],3);
$response = json_decode($response, true);
```

#### Fetch Vectors

To fetch vectors from an index, you can use the `fetch` method:

```php
$index = $this->client->Index('my-index');
$state = $index->describeIndexStats();

// Fetch vectors
$id = "DEeSvRP8XKZwzpWD";
$response = $index->fetch([$id], 'project-000001');
$response = json_decode($response, true);
```

#### Update Vectors

To update vectors in an index, you can use the `update` method:

```php
$index = $this->client->Index('my-index');
$state = $index->describeIndexStats();

// Update vectors
$id = "DEeSvRP8XKZwzpWD";
$response = $index->update($id, 'project-000001',null,null,['testing' => 'testing5']);
$response = json_decode($response, true);
```

#### Delete Vectors

To delete vectors from an index, you can use the `delete` method:

```php
$index = $this->client->Index('my-index');
$state = $index->describeIndexStats();

// Delete vectors
$id = "DEeSvRP8XKZwzpWD";
$response = $index->delete($id, 'project-000001');
```

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](https://choosealicense.com/licenses/mit/)