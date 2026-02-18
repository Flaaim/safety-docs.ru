import {Htag, Ptag} from "@/components";
import PaymentResultPage from "@/components/PaymentResult/PaymentResult";
import Link from "next/link";



export default async function Result(){

  return (

    <div >
      <Htag tag='h2'>Результаты оплаты</Htag>
      <Ptag>
        Если вы по ошибке указали неверный адрес электронной почты, вы можете написать <a href="https://t.me/flaaim" target="_blank">мне в телеграм</a>
      </Ptag>
      <PaymentResultPage />
      <Ptag>Вернуться на <Link href='/'>главную</Link></Ptag>
    </div>
  )
}
