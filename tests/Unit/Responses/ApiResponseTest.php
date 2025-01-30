<?php

namespace Tests\Unit\Responses;

use App\Responses\ApiResponse;
use Tests\TestCase;

class ApiResponseTest extends TestCase
{
    /**
     * Test the success response.
     *
     * @return void
     */
    public function testSuccessResponse()
    {
        $message = 'Rrquest successful';
        $data = ['id' => 1, 'name' => 'Test Item'];
        $statusCode = 200;

        $response = ApiResponse::success($message, $data, $statusCode);

        $this->assertEquals($statusCode, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertEquals([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $responseData);
    }

    /**
     * Test the success response with default status code.
     *
     * @return void
     */
    public function testSuccessResponseWithDefaultStatusCode()
    {
        $message = 'Rrquest successful';
        $data = ['id' => 1, 'name' => 'Test Item'];

        $response = ApiResponse::success($message, $data);

        $this->assertEquals(200, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertEquals([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $responseData);
    }

    /**
     * Test the error response.
     *
     * @return void
     */
    public function testErrorResponse()
    {
        $message = 'Rrquest failed';
        $statusCode = 400;
        $data = ['error' => 'Invalid input'];

        $response = ApiResponse::error($message, $statusCode, $data);

        $this->assertEquals($statusCode, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertEquals([
            'status' => 'error',
            'message' => $message,
            'data' => $data,
        ], $responseData);
    }

    /**
     * Test the error response with default status code.
     *
     * @return void
     */
    public function testErrorResponseWithDefaultStatusCode()
    {
        $message = 'Rrquest failed';
        $data = ['error' => 'Invalid input'];

        $response = ApiResponse::error($message, 400, $data);

        $this->assertEquals(400, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertEquals([
            'status' => 'error',
            'message' => $message,
            'data' => $data,
        ], $responseData);
    }

    /**
     * Test the success response with null data.
     *
     * @return void
     */
    public function testSuccessResponseWithNullData()
    {
        $message = 'Rrquest successful';
        $statusCode = 200;

        $response = ApiResponse::success($message, null, $statusCode);

        $this->assertEquals($statusCode, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertEquals([
            'status' => 'success',
            'message' => $message,
            'data' => null,
        ], $responseData);
    }

    /**
     * Test the error response with null data.
     *
     * @return void
     */
    public function testErrorResponseWithNullData()
    {
        $message = 'Rrquest failed';
        $statusCode = 400;

        $response = ApiResponse::error($message, $statusCode, null);

        $this->assertEquals($statusCode, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertEquals([
            'status' => 'error',
            'message' => $message,
            'data' => null,
        ], $responseData);
    }
}
