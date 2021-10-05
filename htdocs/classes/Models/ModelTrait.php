<?php

namespace classes\Models;

trait ModelTrait
{
    /**
     * store into dabase new model with passed arrays of attributes
     *
     * @param array $data
     * @return static
     */
    public static function store(array $data): self
    {
        $model = new static();
        foreach ($data as $k => $v) {
            $model->{$k} = $v;
        }
        $model->save();
        return $model;
    }

    /**
     * return one instance of filtered model from one finded
     *
     * @param string $fieldName
     * @param [type] $fieldValue
     * @return static
     */
    public static function findBy(string $fieldName, $fieldValue)
    {
        return (new static())->findone([$fieldName . '=?', $fieldValue]);
    }

    /**
     * return one insance of filtered model from db
     *
     * @param [type] $filter
     */
    public static function findWhere($fieldName, $condition, $fieldValue = null)
    {
        $result = static::where($fieldName, $condition, $fieldValue);
        return $result ? $result[0] : null;
    }

    /**
     * return array of models filtered from db
     *
     * @param string $fieldName
     * @param [type] $condition
     * @param [type] $fieldValue
     * @return [static]
     */
    public static function where($fieldName, $condition, $fieldValue = null): array
    {
        if ($fieldValue === null) {
            $fieldValue = $condition;
            $condition = '=';
        }
        return (new static())->find([$fieldName . ' ' . $condition . '?', $fieldValue]);
    }

    /**
     * Return array representation of model attributes
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        foreach ($this->attributes as $attributeName) {
            $array[$attributeName] = $this->{$attributeName};
        }
        return $array;
    }

    /**
     * return all models in from database
     *
     * @return [static]
     */
    public static function all(array $option = null)
    {
        return (new static())->find(['id>?', 0], $option);
    }
}
