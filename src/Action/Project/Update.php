<?php

namespace App\Action\Project;

use App\Service;
use Fusio\Engine\ActionInterface;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;
use Fusio\Engine\Response\FactoryInterface;
use PSX\Http\Exception\InternalServerErrorException;
use PSX\Http\Exception\StatusCodeException;

readonly class Update implements ActionInterface
{
    public function __construct(private Service\Project $service, private FactoryInterface $response)
    {
    }

    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context): mixed
    {
        try {
            $message = $this->service->update(
                $request->get('id'),
                $request->getPayload()
            );
        } catch (StatusCodeException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new InternalServerErrorException($e->getMessage());
        }

        return $this->response->build(200, [], $message);
    }
}
