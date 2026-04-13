import {JSX} from "react";
import {DeflisttagProps} from "@/components/DeflistTag/Deflisttag.props";
import styles from './Deflisttag.module.css';
import {DefItemProps} from "@/components/DeflistTag/DefItem.props";

export const DefItem = ({term, definition}: DefItemProps ): JSX.Element => {
  return <>
    <dt className={styles.dt}>{term}</dt>
    <dd className={styles.dd}>{definition}</dd>
  </>;
};

export const Deflisttag = ({children, ...props}: DeflisttagProps): JSX.Element => {
  return (<dl className={styles.dl} {...props}>
      {children}
  </dl>);
};
