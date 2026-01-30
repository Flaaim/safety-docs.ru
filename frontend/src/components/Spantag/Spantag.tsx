import {JSX} from "react";
import {SpantagProps} from "./Spantag.props";
import cn from 'classnames'
import styles from './Spantag.module.css'

export const Spantag = ({size = 's', children, className}: SpantagProps): JSX.Element => {
  return (<span
    className={cn(className, {
      [styles.s]: size === 's',
      [styles.m]: size === 'm',
    })}
  >{children}
  </span>)
}
