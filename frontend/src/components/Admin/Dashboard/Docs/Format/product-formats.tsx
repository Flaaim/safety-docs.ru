"use client";

import {useEffect, useState} from "react";
import { Button } from "@/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import {ChevronDown} from "lucide-react";
import {formatsProduct} from "@/interfaces/product.interface";

export interface ProductMultipleFormatsProps {
  formats: string[];
  onChange?: (formats: string[]) => void;
}

export function ProductMultipleFormats({ formats = [], onChange }: ProductMultipleFormatsProps) {
  const [selectedValues, setSelectedValues] = useState<string[]>(formats);

  useEffect(() => {
    setSelectedValues(formats);
  }, [formats]);


  const handleSelect = (value: string) => {
    const newValues = selectedValues.includes(value)
      ? selectedValues.filter(v => v !== value)
      : [...selectedValues, value];

    setSelectedValues(newValues);

    if (onChange) {
      onChange(newValues);
    }
  };
  return (
    <div onClick={(e) => e.stopPropagation()}>
      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button variant="outline" className="w-full justify-between">
            {selectedValues.length > 0
              ? `Выбрано: ${selectedValues.length}`
              : "Выберите формат"}
            <ChevronDown className="h-4 w-4 ml-2" />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent
          className="w-56"
          sideOffset={5}
          align="start"
        >
          {formatsProduct.map((format) => (
            <DropdownMenuCheckboxItem
              key={format}
              checked={selectedValues.includes(format)}
              onCheckedChange={() => handleSelect(format)}
            >
              {format.toUpperCase()}
            </DropdownMenuCheckboxItem>
          ))}
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  );
}


