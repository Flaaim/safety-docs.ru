import {HeaderProps} from "@/components/Header/Header.props";
import {JSX} from "react";
import cn from 'classnames'
import styles from './Header.module.css'
import Link from "next/link";

export const Header = ({className}:HeaderProps): JSX.Element => {
  return <header className={cn(className, styles.header)}>
    <nav>
        <Link href="/" className={cn(className, styles.link)}>Главная</Link>
        <Link href="/safety" className={cn(className, styles.link)}>Охрана труда</Link>
    </nav>
  </header>
}
