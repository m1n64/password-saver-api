<?php


namespace App\Traits;


use App\Classes\Constants\StatusCodes;
use Nette\Utils\Json;

trait JsonResponseSocketTrait
{
    use JsonResponseTrait;

    /**
     * @param string $message
     * @param array $data
     * @param int $status
     * @return string
     * @throws \Nette\Utils\JsonException
     */
    protected function success(string $message, $data = [], int $status = StatusCodes::SUCCESS)
    {
        return Json::encode($this->getAnswer(true, $message, $data));
    }

    protected function error(string $message, int $status = StatusCodes::SUCCESS, $data = [])
    {
        return Json::encode($this->getAnswer(false, $message, $data));
    }
}
