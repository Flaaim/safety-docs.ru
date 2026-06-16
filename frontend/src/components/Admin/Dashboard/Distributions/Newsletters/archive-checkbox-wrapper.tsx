"use client";

import { usePathname, useRouter, useSearchParams } from "next/navigation";
import { Checkbox } from "@/components/ui/checkbox";
import { Field } from "@/components/ui/field";
import { Label } from "@/components/ui/label";

interface ArchiveCheckboxWrapperProps {
  isChecked: boolean;
}

export function ArchiveCheckboxWrapper({ isChecked }: ArchiveCheckboxWrapperProps) {
  const router = useRouter();
  const pathname = usePathname();
  const searchParams = useSearchParams();

  const handleChange = (checked) => {
    const params = new URLSearchParams(searchParams);
    if (checked) {
      params.set("archive", "true");
    } else {
      params.delete("archive");
    }
    router.push(`${pathname}?${params.toString()}`);
  };

  return (
    <Field orientation="horizontal">
      <Checkbox id="archive-newsletters" checked={isChecked} onCheckedChange={handleChange} />
      <Label htmlFor="archive-newsletters">Показать архивные рассылки</Label>
    </Field>
  );
}
