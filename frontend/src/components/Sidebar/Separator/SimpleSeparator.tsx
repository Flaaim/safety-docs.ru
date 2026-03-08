import { Separator } from "@/components/ui/separator"
import Link from "next/link";

export function SimpleSeparator() {
  return (
    <div className="flex max-w-sm flex-col gap-4 text-sm text-center">
      <div className="flex flex-col gap-1.5">
        <div className="leading-none font-medium">Блог охраны труда</div>
        <div className="text-muted-foreground">
          <Link href='https://t.me/help_ot_news'>https://t.me/help_ot_news</Link>
        </div>
      </div>
      <Separator />
      <div>
        Информационная поддержка проекта "Блог охраны труда".
      </div>
    </div>
  )
}
