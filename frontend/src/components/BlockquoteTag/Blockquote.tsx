import {JSX} from "react";
import styles from './Blockquote.module.css'
import {Ptag} from "@/components";
export const Blockquote = ({children}): JSX.Element => {
  return <blockquote className={styles.blockquote}>
    {children}
  </blockquote>
}
