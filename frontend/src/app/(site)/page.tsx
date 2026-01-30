import {Blockquote, Htag, Listtag, Ptag} from "@/components";


export default function Home() {
  return (
    <div >
      <Ptag   >Стандартный размер шрифта</Ptag>
      <Ptag  size='s'>Маленький размер шрифта</Ptag>
      <Ptag  appearance='bold'>Жирный шрифт</Ptag>
      <Ptag  appearance='italic' size='s'>Италик шрифт</Ptag>
      <Ptag  appearance='strikethrough'>Зачеркнутый шрифт</Ptag>
      <Htag tag='h1'>Header 1</Htag>
      <Ptag>This is a normal paragraph following a header. GitHub is a code hosting platform for version control and collaboration. It lets you and others work together on projects from anywhere.</Ptag>

      <Blockquote>When something is important enough, you do it even if the odds are not in your favor.</Blockquote>

    </div>
  );
}
