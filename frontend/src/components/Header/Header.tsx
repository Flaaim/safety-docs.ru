import { HeaderProps } from "@/components/Header/Header.props";
import { cache, JSX } from "react";
import cn from "classnames";
import styles from "./Header.module.css";
import Link from "next/link";
import { getAllDirections } from "@api/direction";

const getCachedDirections = cache(async () => {
  return await getAllDirections();
});

export const Header = async ({ className }: HeaderProps): Promise<JSX.Element> => {
  const data = await getCachedDirections();
  return (
    <header className={cn(className, styles.header)}>
      <nav>
        <Link href="/" className={cn(className, styles.link)}>
          Главная
        </Link>
        {data.directions.map((direction) => {
          return (
            <Link
              key={direction.id}
              href={"/docs/" + direction.slug}
              className={cn(className, styles.link)}
            >
              {direction.title}
            </Link>
          );
        })}
      </nav>
    </header>
  );
};
