<?php


namespace App\Entity;


class AdSearch{

    private $field;

    /**
     * @return string|null
     */
    public function getField(): ?string
    {
        return $this->field;
    }

    /**
     * @param string|null $field
     * @return AdSearch
     */
    public function setField(?string $field) : AdSearch
    {
        $this->field = $field;
        return $this;
    }


}