<?php

namespace Service;

class Dto
{
    private array $items = [];

    /**
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): self {
        $dto = new static();
        $dto->loadArray($data);
        return $dto;
    }

    public function toArray(): array
    {
        return $this->isComposite()
            ? array_map(fn(self $dto) => $dto->toArray(), $this->items)
            : $this->itemToArray();
    }

    public function loadArray(array $data): void
    {
        if (count($data) === 0) return;
        if (isset($data[0])) {
            $this->items = array_map(fn(array $itemData) => self::fromArray($itemData), $data);
        } else {
            $this->itemLoadArray($data);
        }
    }

    private function itemToArray(): array
    {
        $result = [];
        foreach(get_object_vars($this) as $k => $v) {
            if (is_scalar($v)) {
                $result[$k] = $v;
            }
        }
        return $result;
    }

    private function itemLoadArray(array $data): void
    {
        foreach ($data as $k => $v) {
            if (property_exists($this, $k) && is_scalar($v)) {
                $this->{$k} = $v;
            }
        }
    }

    public function get(int $idx)
    {
        return $this->items[$idx];
    }

    public function add(Dto $dto)
    {
        $this->items[] = $dto;
    }

    public function delete(int $idx): void
    {
        array_splice($this->items, $idx, 1);
    }

    public function clean()
    {
        $this->items = [];
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isComposite(): bool
    {
        return $this->count() > 0;
    }
}