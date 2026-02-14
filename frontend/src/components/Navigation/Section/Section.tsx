import {JSX} from "react";
import cn from "classnames";
import styles from './Section.module.css'
import classNames from "classnames";
import {SectionProps} from "@/components/Navigation/Section/Section.props";
import {Htag} from "@/components";
export const Section = ({icon, title}: SectionProps): JSX.Element => {
  return <div className={cn(classNames, styles.section)}>
    <div className={styles.icon}>
      {icon}
    </div>
    <Htag tag='h4'>{title}</Htag>
    <span>Смотреть</span>
  </div>
}
