import {HeaderProps} from "@/components/Header/Header.props";
import {JSX} from "react";
import cn from 'classnames'
import styles from './Header.module.css'

export const Header = ({className}:HeaderProps): JSX.Element => {
  return <header className={cn(className, styles.header)}>
    <nav>
      <div className="logo">
        <a href="/">Главная</a>
      </div>
    </nav>

  </header>
}
