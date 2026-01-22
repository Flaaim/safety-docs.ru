import {JSX} from "react";
import {PtagProps} from "@/components/Ptag/Ptag.props";
import styles from './Ptag.module.css';
import cn from "classnames";
export const Ptag = ({size = 'm', className, children, ...props}: PtagProps):JSX.Element => {
  return (
    <p
      className={cn(styles.p, className, {
        [styles.s]: size === 's',
        [styles.m]: size === 'm'
      })}
      {...props}
    >
      {children}
    </p>
  );
};
