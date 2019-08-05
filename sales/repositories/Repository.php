<?php

namespace sales\repositories;

class Repository
{
    public function __call($name, $arguments)
    {
        $prefix = substr($name, 0, 3);
        if ($prefix === 'get') {
            $method = 'find' . substr($name, 3, strlen($name));
            if (method_exists($this, $method)) {
                try {
                    return call_user_func_array([$this, $method], $arguments);
                } catch (NotFoundException $e) {
                    return null;
                } catch (\Throwable $e) {
                    throw new $e;
                }
            } else {
                throw new \BadMethodCallException('Instance methods ' . static::class . '->' . $name . ' and ' . static::class . '->' . $method . '  doesn\'t exists');
            }
        }
        throw new \BadMethodCallException('Instance method ' . static::class . '->' . $name . ' doesn\'t exist');
    }
}