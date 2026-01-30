import {JSX} from "react";
import {SpantagProps} from "./Spantag.props";
import cn from 'classnames'
import styles from './Spantag.module.css'

export const Spantag = ({size = 's', appearance, children, className}: SpantagProps): JSX.Element => {
  return (<span
    className={cn(className, {
      [styles.s]: size === 's',
      [styles.m]: size === 'm',
      [styles.bold]: appearance === 'bold',
      [styles.italic]: appearance === 'italic',
      [styles.strikethrough]: appearance === 'strikethrough'
    })}
  >{children}
  </span>)
}
