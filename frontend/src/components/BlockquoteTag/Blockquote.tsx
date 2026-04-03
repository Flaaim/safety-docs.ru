import {JSX} from "react";
import styles from './Blockquote.module.css';
import {BlockquoteProps} from "./Blockquote.props";

export const Blockquote = ({children}: BlockquoteProps): JSX.Element => {
  return <blockquote className={styles.blockquote}>
    {children}
  </blockquote>;
};
