<?php
namespace AbstractSources;

abstract class AbstractApi
{
    public function outputJson($data)
    {
        header("Content-type: application/json");
		exit(json_encode($data));
    }
}
