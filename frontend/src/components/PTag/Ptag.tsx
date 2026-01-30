import {JSX} from "react";
import {PtagProps} from "@/components/Ptag/Ptag.props";
import styles from './Ptag.module.css';
import cn from "classnames";
export const Ptag = ({size = 'm', appearance, className, children, ...props}: PtagProps):JSX.Element => {
  return (
    <p
      className={cn(styles.p, className, {
        [styles.s]: size === 's',
        [styles.m]: size === 'm',
        [styles.bold]: appearance === 'bold',
        [styles.italic]: appearance === 'italic',
        [styles.strikethrough]: appearance === 'strikethrough',
      })}
      {...props}
    >
      {children}
    </p>
  );
};
