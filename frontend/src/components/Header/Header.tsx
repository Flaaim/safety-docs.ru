import {HeaderProps} from "@/components/Header/Header.props";
import {JSX} from "react";
import cn from 'classnames'
import styles from './Header.module.css'

export const Header = ({className}:HeaderProps): JSX.Element => {
  return <header className={cn(className, styles.header)}>
    <nav>
        <a href="/" className={cn(className, styles.link)}>Главная</a>
    </nav>
  </header>
}
