<?php

namespace Islambaraka90\PineconePhpClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PineconePhpClient
{
    protected $api_key;
    protected $environment;

    public function __construct($api_key, $environment = 'us-east1-gcp') {
        $this->api_key = $api_key;
        $this->environment = $environment;
    }

    public function Index($name)
    {
        // Create a new instance of the Index class, passing in the name of the index and the default environment
        return new Index($name, $this->api_key , $this->environment);
    }

    function listIndexes() {
        $client = new Client([
            'base_uri' => 'https://controller.'.$this->environment.'.pinecone.io',
            'timeout'  => 30,
            'headers' => [
                'Api-Key' => $this->api_key,
                'Accept' => 'application/json; charset=utf-8'
            ]
        ]);

        try {
            $response = $client->get('/databases');
            return $response->getBody()->getContents();
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return $e->getResponse()->getBody()->getContents();
            } else {
                return "An error occurred: " . $e->getMessage();
            }
        }
    }

    function createIndex($name, $dimension, $metric = 'cosine', $metadata_config = null ,  $pods = 1, $replicas = 1, $pod_type = 'p1.x1') {
        $client = new Client([
            'base_uri' => 'https://controller.'.$this->environment.'.pinecone.io',
            'timeout'  => 30,
            'headers' => [
                'Api-Key' => $this->api_key,
                'Accept' => 'application/json; charset=utf-8',
                'Content-Type' => 'application/json'
            ]
        ]);

        $params = [
            'name' => $name,
            'dimension' => $dimension,
            'metric' => $metric,
            'pods' => $pods,
            'replicas' => $replicas,
            'pod_type' => $pod_type,
            'metadata_config' => $metadata_config,
        ];

        try {
            $response = $client->post('/databases', [
                'json' => $params
            ]);

            if ($response->getStatusCode() == 201) {
                return "The index has been successfully created";
            } else {
                return "An error occurred: " . $response->getBody()->getContents();
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                if ($response->getStatusCode() == 400) {
                    return "Bad request. Encountered when request exceeds quota or an invalid index name.";
                } elseif ($response->getStatusCode() == 409) {
                    return "Index of given name already exists.";
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


    function describeIndex($name) {
        $client = new Client([
            'base_uri' => 'https://controller.'.$this->environment.'.pinecone.io',
            'timeout'  => 30,
            'headers' => [
                'Api-Key' => $this->api_key,
                'Accept' => 'application/json; charset=utf-8',
                'Content-Type' => 'application/json'
            ]
        ]);

        try {
            $response = $client->get('/databases/'.$name);

            if ($response->getStatusCode() == 200) {
                return $response->getBody()->getContents();
            } elseif ($response->getStatusCode() == 404) {
                return "Index not found";
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



    function deleteIndex($name) {
        $client = new Client([
            'base_uri' => 'https://controller.'.$this->environment.'.pinecone.io',
            'timeout'  => 30,
            'headers' => [
                'Api-Key' => $this->api_key,
                'Accept' => 'text/plain'
            ]
        ]);

        try {
            $response = $client->delete('/databases/'.$name);

            if ($response->getStatusCode() == 202) {
                return "Index successfully deleted.";
            } elseif ($response->getStatusCode() == 404) {
                return "Index not found.";
            } else {
                return "An error occurred: " . $response->getBody()->getContents();
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                if ($response->getStatusCode() == 404) {
                    return "Index not found.";
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



    function configureIndex($name, $replicas, $pod_type) {
        $client = new Client([
            'base_uri' => 'https://controller.'.$this->environment.'.pinecone.io',
            'timeout'  => 30,
            'headers' => [
                'Api-Key' => $this->api_key,
                'Accept' => 'text/plain',
                'Content-Type' => 'application/json'
            ]
        ]);

        $body = json_encode([
            'replicas' => $replicas,
            'pod_type' => $pod_type
        ]);

        try {
            $response = $client->patch('/databases/'.$name, ['body' => $body]);

            if ($response->getStatusCode() == 202) {
                return "Index configuration successfully updated.";
            } elseif ($response->getStatusCode() == 404) {
                return "Index not found.";
            } elseif ($response->getStatusCode() == 400) {
                return "Bad request, not enough quota.";
            } else {
                return "An error occurred: " . $response->getBody()->getContents();
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                if ($response->getStatusCode() == 404) {
                    return "Index not found.";
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



    /*
     * Collections methods
     */



    function listCollections() {
        $client = new Client([
            'base_uri' => 'https://controller.'.$this->environment.'.pinecone.io',
            'timeout'  => 30,
            'headers' => [
                'Api-Key' => $this->api_key,
                'Accept' => 'application/json; charset=utf-8'
            ]
        ]);

        try {
            $response = $client->get('/collections');

            if ($response->getStatusCode() == 200) {
                $body = $response->getBody()->getContents();
                return json_decode($body);
            } else {
                return "An error occurred: " . $response->getBody()->getContents();
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                if ($response->getStatusCode() == 500) {
                    return "Internal error. Can be caused by invalid parameters.";
                } else {
                    return "An error occurred: " . $e->getMessage();
                }
            } else {
                return "An error occurred: " . $e->getMessage();
            }
        }
    }


    function createCollection($name, $source) {
        $client = new Client([
            'base_uri' => 'https://controller.'.$this->environment.'.pinecone.io',
            'timeout'  => 30,
            'headers' => [
                'Api-Key' => $this->api_key,
                'Accept' => 'text/plain',
                'Content-Type' => 'application/json'
            ]
        ]);

        $body = json_encode([
            'name' => $name,
            'source' => $source
        ]);

        try {
            $response = $client->post('/collections', [
                'body' => $body
            ]);

            if ($response->getStatusCode() == 201) {
                return "Collection successfully created.";
            } elseif ($response->getStatusCode() == 409) {
                return "A collection with the name provided already exists.";
            } else {
                return "An error occurred: " . $response->getBody()->getContents();
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                if ($response->getStatusCode() == 400) {
                    return "Bad request. Request exceeds quota or collection name is invalid.";
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



    function describeCollection($name) {
        $client = new Client([
            'base_uri' => 'https://controller.'.$this->environment.'.pinecone.io',
            'timeout'  => 30,
            'headers' => [
                'Api-Key' => $this->api_key,
                'Accept' => 'application/json',
            ]
        ]);

        try {
            $response = $client->get('/collections/' . $name);

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody(), true);
            } elseif ($response->getStatusCode() == 404) {
                return "Index not found.";
            } else {
                return "An error occurred: " . $response->getBody()->getContents();
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                if ($response->getStatusCode() == 500) {
                    return "Internal error. Can be caused by invalid parameters.";
                } else {
                    return "An error occurred: " . $e->getMessage();
                }
            } else {
                return "An error occurred: " . $e->getMessage();
            }
        }
    }


    function deleteCollection($name) {
        $client = new Client([
            'base_uri' => 'https://controller.'.$this->environment.'.pinecone.io',
            'timeout'  => 30,
            'headers' => [
                'Api-Key' => $this->api_key,
                'Accept' => 'text/plain',
            ]
        ]);

        try {
            $response = $client->delete('/collections/' . $name);

            if ($response->getStatusCode() == 202) {
                return "Collection has been successfully deleted.";
            } elseif ($response->getStatusCode() == 404) {
                return "Collection not found.";
            } else {
                return "An error occurred: " . $response->getBody()->getContents();
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                if ($response->getStatusCode() == 500) {
                    return "Internal error. Can be caused by invalid parameters.";
                } else {
                    return "An error occurred: " . $e->getMessage();
                }
            } else {
                return "An error occurred: " . $e->getMessage();
            }
        }
    }


}