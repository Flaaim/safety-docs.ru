import {JSX} from "react";
import {ListtagProps} from "./Listtag.props";
import cn from 'classnames'
import styles from './Listtag.module.css'

export const Listtag = ({children, appearance = 'ul', className, ...props}: ListtagProps): JSX.Element => {
  const Tag = appearance === 'ul' ? 'ul' : 'ol';

  return (
    <Tag className={cn(
        appearance === 'ul' ? styles.ul : styles.ol,
        className
      )}
      {...props}
    >
      {children}
    </Tag>
  );
}
