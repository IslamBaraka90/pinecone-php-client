<?php

namespace Islambaraka90\PineconePhpClient;

use GuzzleHttp\Client;

/**
The Index class is a subclass of PineconePhpClient that represents a Pinecone index.
@package Islambaraka90\PineconePhpClient
 */
class Index extends PineconePhpClient
{
    /**
    The name of the index.
    @var string
     */
    protected $name;
    /**
    The host address of the index.
    @var string
     */
    protected $host;

    /**
     * Constructs an Index instance with the specified name, API key, and environment.
     *
     * @param string $name The name of the Index.
     * @param string $api_key The API key to use for authentication.
     * @param string $environment The environment to use (e.g. "production" or "development").
     *
     * @throws Exception if the specified Index is not found.
     */
    public function __construct($name, $api_key, $environment) {
        parent::__construct($api_key, $environment);
        $this->name = $name;
        $description = $this->describe();
        if ($description === "Index not found") {
            throw new \Exception("Index not found");
        }
        $description = json_decode($description, true);
        $this->host = $description['status']['host'];
    }

    /**
     * Returns a JSON string containing information about the index.
     * @return string|false A JSON string containing information about the index, or false on failure.
     */
    public function describe()
    {
        // Call the describeCollection method in the parent class, passing in the name of the index
        return $this->describeIndex($this->name);
    }

    /**
     * Returns statistics about the index.
     *
     * @param array $filter An optional array of filters to apply to the statistics.
     * @return string A JSON string containing statistics about the index.
     * @throws Exception If the index is not found or if an error occurs.
     */
    public function describeIndexStats($filter = []) {
        $client = new Client([
            'base_uri' => "https://".$this->host,
            'timeout'  => 30,
            'headers' => [
                'Api-Key' => $this->api_key,
                'Accept' => 'application/json; charset=utf-8',
                'Content-Type' => 'application/json'
            ]
        ]);


        $payload = ['filter' => $filter];


        try {
            $send_data = !empty($filter)?[
                'json' => $payload
            ]: [];
            $response = $client->post('/describe_index_stats', $send_data);

            if ($response->getStatusCode() == 200) {
                return $response->getBody()->getContents();
            } else {
                return "An error occurred: " . $response->getBody()->getContents();
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                if ($response->getStatusCode() == 404) {
                    return "Index not found";
                } elseif ($response->getStatusCode() == 500) {
                    return "Internal error. Can be caused by invalid parameters.";
                } else {
                    return "An error occurred: " . $e->getMessage();
                }
            } else {
                return "An error occurred: " . $e->getMessage();
            }
        }
    }



    /**
     * Upsert a set of vectors in a specified namespace.
     *
     * @param string $namespace The namespace to upsert the vectors into.
     * @param array $vectors An array of vectors to upsert.
     * @return mixed|string Returns the API response on success, or an error message on failure.
     */
    public function upsert($namespace, $vectors) {
        $client = new Client([
            'base_uri' => "https://".$this->host,
            'timeout'  => 30,
            'headers' => [
                'Api-Key' => $this->api_key,
                'Accept' => 'application/json; charset=utf-8',
                'Content-Type' => 'application/json'
            ]
        ]);

        $payload = [
            'namespace' => $namespace,
            'vectors' => $vectors
        ];

        try {
            $response = $client->post('/vectors/upsert', [
                'json' => $payload
            ]);

            if ($response->getStatusCode() == 200) {
                return $response->getBody()->getContents();
            } else {
                return "An error occurred: " . $response->getBody()->getContents();
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                return "An error occurred: " . $response->getBody()->getContents();
            } else {
                return "An error occurred: " . $e->getMessage();
            }
        }
    }

    /**
     * Query for the top K vectors nearest to a given vector in a specified namespace.
     *
     * @param string $namespace The namespace to query the vectors in.
     * @param array $vector The vector to query against.
     * @param array $filter Optional filters to apply to the query.
     * @param int $topK The number of nearest vectors to return.
     * @param bool $includeMetadata Whether to include metadata for the returned vectors.
     * @param bool $includeVector Whether to include the vector data for the returned vectors.
     * @return mixed|string Returns the API response on success, or an error message on failure.
     */
    public function query($namespace, $vector,$filter = [] ,  $topK = 3,$includeMetadata = true ,$includeVector = false ) {
        $client = new Client([
            'base_uri' => "https://".$this->host,
            'timeout'  => 30,
            'headers' => [
                'Api-Key' => $this->api_key,
                'Accept' => 'application/json; charset=utf-8',
                'Content-Type' => 'application/json'
            ]
        ]);

        $payload = [
            'namespace' => $namespace,
            'vector' => $vector,
            'topK' => $topK,
            'includeMetadata' => $includeMetadata,
            'includeVector' => $includeVector

        ];
        // If filter is not empty, add it to the payload
        if (!empty($filter)) {
            $payload['filter'] = $filter;
        }


        try {
            $response = $client->post('/query', [
                'json' => $payload
            ]);

            if ($response->getStatusCode() == 200) {
                return $response->getBody()->getContents();
            } else {
                return "An error occurred: " . $response->getBody()->getContents();
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                return "An error occurred: " . $response->getBody()->getContents();
            } else {
                return "An error occurred: " . $e->getMessage();
            }
        }
    }


    /**
     * Fetches vectors by ids for a given namespace.
     *
     * @param array $ids The list of ids for which to fetch vectors.
     * @param string $namespace The namespace from which to fetch vectors.
     * @return string The response from the API call, or an error message if an error occurred.
     */
    public function fetch($ids , $namespace) {
        $client = new Client([
            'base_uri' => "https://".$this->host,
            'timeout'  => 30,
            'headers' => [
                'Api-Key' => $this->api_key,
                'Accept' => 'application/json; charset=utf-8',
                'Content-Type' => 'application/json'
            ]
        ]);

        $payload = [
            'ids' => $ids
        ];

        try {
            $response = $client->get('/vectors/fetch', [
                'query' => ['namespace' => $namespace],
                'json' => $payload
            ]);

            if ($response->getStatusCode() == 200) {
                return $response->getBody()->getContents();
            } else {
                return "An error occurred: " . $response->getBody()->getContents();
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                return "An error occurred: " . $response->getBody()->getContents();
            } else {
                return "An error occurred: " . $e->getMessage();
            }
        }
    }



    /**
     * Deletes vectors by ids for a given namespace.
     *
     * @param array $ids The list of ids for which to delete vectors.
     * @param string $namespace The namespace from which to delete vectors.
     * @param bool $deleteAll Whether to delete all vectors in the namespace.
     * @param mixed $filter An optional filter to apply when deleting vectors.
     * @return string The response from the API call, or an error message if an error occurred.
     */
    public function delete($ids, $namespace, $deleteAll = false, $filter = null) {
        $client = new Client([
            'base_uri' => "https://".$this->host,
            'timeout'  => 30,
            'headers' => [
                'Api-Key' => $this->api_key,
                'Accept' => 'application/json; charset=utf-8',
                'Content-Type' => 'application/json'
            ]
        ]);

        $payload = [
            'ids' => $ids,
            'deleteAll' => $deleteAll,
            'namespace' => $namespace,
        ];
        // If filter is not empty, add it to the payload
        if (!empty($filter)) {
            $payload['filter'] = $filter;
        }

        try {
            $response = $client->post('/vectors/delete', [
                'json' => $payload
            ]);

            if ($response->getStatusCode() == 200) {
                return $response->getBody()->getContents();
            } else {
                return "An error occurred: " . $response->getBody()->getContents();
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                return "An error occurred: " . $response->getBody()->getContents();
            } else {
                return "An error occurred: " . $e->getMessage();
            }
        }
    }

    /**
     * Updates the vector for a given id and namespace.
     *
     * @param string $id The id of the vector to update.
     * @param string $namespace The namespace in which the vector is located.
     * @param mixed $values An optional array of values to update the vector with.
     * @param mixed $sparseValues An optional array of sparse values to update the vector with.
     * @param mixed $setMetadata An optional metadata object to set on the vector.
     * @return string The response from the API call, or an error message if an error occurred.
     */
    public function update($id,  $namespace, $values = null, $sparseValues = null, $setMetadata = null) {
        $client = new Client([
            'base_uri' => "https://".$this->host,
            'timeout'  => 30,
            'headers' => [
                'Api-Key' => $this->api_key,
                'Accept' => 'application/json; charset=utf-8',
                'Content-Type' => 'application/json'
            ]
        ]);

        $payload = [
            'id' => $id,
            'namespace' => $namespace
        ];

        if ($values !== null) {
            $payload['values'] = $values;
        }

        if ($sparseValues !== null) {
            $payload['sparseValues'] = $sparseValues;
        }

        if ($setMetadata !== null) {
            $payload['setMetadata'] = $setMetadata;
        }

        try {
            $response = $client->post('/vectors/update', [
                'json' => $payload
            ]);

            if ($response->getStatusCode() == 200) {
                return $response->getBody()->getContents();
            } else {
                return "An error occurred: " . $response->getBody()->getContents();
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                return "An error occurred: " . $response->getBody()->getContents();
            } else {
                return "An error occurred: " . $e->getMessage();
            }
        }
    }



}