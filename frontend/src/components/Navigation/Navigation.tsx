import {JSX} from "react";
import styles from './Navigation.module.css'

export const Navigation = ({children}): JSX.Element => {
  return <div className={styles.navigation}>
    {children}
  </div>
}
