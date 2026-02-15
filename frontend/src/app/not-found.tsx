import {Htag} from "@/components";
import Link from "next/link";
import type {Metadata} from "next";

export const metadata: Metadata = {
  title: "Страница не найдена",
  description: "Запрашиваемая страница не может быть найдена...",
};
export default function NotFound(){
  return (<div>
        <Htag tag='h2'>Страница не найдена</Htag>
        <p>Запрашиваемая страница не может быть найдена...</p>
        <Link href='/'>Вернуться на главную</Link>
  </div>)
}
