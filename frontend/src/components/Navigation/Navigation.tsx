import {JSX} from "react";
import styles from './Navigation.module.css'
import {NavigationProps} from "@/components/Navigation/Navigation.props";

export const Navigation = ({children}: NavigationProps): JSX.Element => {
  return <div className={styles.navigation}>
    {children}
  </div>
}
