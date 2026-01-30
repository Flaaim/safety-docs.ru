import {JSX} from "react";
import {TagProps} from "@/components/DownloadTag/Tag.props";
import cn from 'classnames'
import styles from './Tag.module.css'

export const Tag = ({children, href, ...props}: TagProps): JSX.Element => {
  return (<div className={cn(styles.tag)}
    {...props}
  >
    {href ? <a href={href}>{children}</a> : <>{children}</>}
  </div>)
}
