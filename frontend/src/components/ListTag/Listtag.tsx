import {JSX} from "react";
import {ListtagProps} from "./Listtag.props";
import cn from 'classnames'
import styles from './Listtag.module.css'

export const Listtag = ({children, appearance = 'ul', className, ...props}: ListtagProps): JSX.Element => {
  const childArray = Array.isArray(children) ? children : [children];
  return (
    <ul
      className={cn(styles.ul, className, {
        [styles.ul]: appearance === 'ul',
        [styles.ol]: appearance === 'ol'
      })}
      {...props}
    >
      {childArray.map((item, index) => (
        <li key={index}>{item}</li>
      ))}
    </ul>
  )
}
