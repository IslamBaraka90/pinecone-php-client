<?php

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Islambaraka90\PineconePhpClient\PineconePhpClient;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../load_env.php';

class PineconePhpClientTest extends TestCase
{
    protected $client;
    private $api_key;
    private $environment;

    protected function setUp(): void {
        $this->api_key =  $_ENV['PINCONE_KEY'] ?: '';
        $this->environment = $_ENV['PINCONE_ENV'] ?: 'us-east1-gcp';
        $this->client = new PineconePhpClient('' . $this->api_key . '', '' . $this->environment . '');
    }

    protected function tearDown(): void {
        $this->client = null;
    }

    public function testListIndexesSuccess() {
        $response = $this->client->listIndexes();
        $this->assertIsString($response);
        $this->assertNotEmpty($response);
    }

    public function testCreateIndexSuccess()
    {
        $name = 'my-index';
        $dimension = 1536;
        $result = $this->client->createIndex($name, $dimension);
        $this->assertEquals('The index has been successfully created', $result);
        $this->assertContainsEquals($result,['The index has been successfully created','Bad request. Encountered when request exceeds quota or an invalid index name.'], $result);

    }

    public function testIndexSuccess() {
        $index = $this->client->Index('my-index');
        $state = $index->describeIndexStats();
        $this->assertIsString($state);
        $this->assertNotEmpty($state);
    }

    public function testIndexUpsertSuccess() {
        $index = $this->client->Index('my-index');
        $state = $index->describeIndexStats();
        // upsert 20 vectors
        $vectorsFile = __DIR__ . '/vectors_objects.json';
        $vectors = json_decode(file_get_contents($vectorsFile), true);
        $project_id = 'project-0000'.rand(1,99);
        $response = $index->upsert($project_id,$vectors['vectors']);
        $this->assertIsString($response);
        $this->assertNotEmpty($response);
        $this->assertEquals('{"upsertedCount":20}', $response);
    }



    public function testIndexQuerySuccess() {
        $index = $this->client->Index('my-index');
        $state = $index->describeIndexStats();
        $vectorsFile = __DIR__ . '/query.json';
        $vectors = json_decode(file_get_contents($vectorsFile), true);
        $response = $index->query('project-000001',$vectors, [],3);
        $response = json_decode($response, true);
        $this->assertIsArray($response);
    }

    public function testIndexFetchSuccess() {
        $index = $this->client->Index('my-index');
        $state = $index->describeIndexStats();
        $id = "DEeSvRP8XKZwzpWD";
        $response = $index->fetch([$id], 'project-000001');
        $response = json_decode($response, true);
        $this->assertIsArray($response);
    }

    public function testIndexUpdateSuccess() {
        $index = $this->client->Index('my-index');
        $state = $index->describeIndexStats();
        $id = "DEeSvRP8XKZwzpWD";
        $response = $index->update($id, 'project-000001',null,null,['testing' => 'testing5']);
        $response = json_decode($response, true);
        $this->assertIsArray($response);
    }

    public function testIndexDeleteSuccess() {
        $index = $this->client->Index('my-index');
        $state = $index->describeIndexStats();
        $id = "DEeSvRP8XKZwzpWD";
        $response = $index->delete($id, 'project-000001');
        $response = json_decode($response, true);
        $this->assertIsArray($response);
    }

    public function testDescribeIndexSuccess() {
        $response = $this->client->describeIndex('my-index');
        $this->assertIsString($response);
        $this->assertNotEmpty($response);
        $this->isJson($response);
    }


    public function testDeleteIndexSuccess() {
        $response = $this->client->deleteIndex('my-index');
        $this->assertIsString($response);
        $this->assertNotEmpty($response);
        $this->isJson($response);
    }





    public function testCreateIndexBadRequest()
    {
        $name = 'invalid%name';
        $dimension = 100;
        $result = $this->client->createIndex($name, $dimension);
        $this->assertContainsEquals($result,['Bad request. Encountered when request exceeds quota or an invalid index name.'], $result);

    }

    public function testCreateIndexConflict()
    {
        $name = 'existing-index';
        $dimension = 100;
        $result = $this->client->createIndex($name, $dimension);
        $this->assertContainsEquals($result,['Index of given name already exists.','Bad request. Encountered when request exceeds quota or an invalid index name.'], $result);
    }

    public function testCreateIndexInternalServerError()
    {
        $name = 'my-index';
        $dimension = -1;
        $result = $this->client->createIndex($name, $dimension);
        $this->assertContainsEquals($result, ['Internal error. Can be caused by invalid parameters.', 'Bad request. Encountered when request exceeds quota or an invalid index name.']);
    }

}

