import React from 'react';
import { Link } from 'react-router-dom';
import Logo from '../../Assets/Img/Logo_Estrela10_Padrao.png';
import './Style.css';

interface HeaderProps {
    title: string;
}

const Header: React.FC<HeaderProps> = (props)=>{
    return (
        <div>
            <header>
                <nav className="navbar navbar-expand-lg navbar-dark bg-dark">
                    <div className="container">
                        <a className="navbar-brand" href="/">Cotação E10</a>
                        <button className="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span className="navbar-toggler-icon"></span>
                        </button>
                        <div className="collapse navbar-collapse justify-content-end"  id="navbarSupportedContent">
                            <ul className="navbar-nav">
                                <li className="nav-item">
                                    <Link to="/" className="nav-link">Cotação de produtos</Link>
                                </li>
                                <li className="nav-item">
                                    <Link to="/simula-produto" className="nav-link">Simular produto</Link>
                                </li>
                                <li className="nav-item">
                                    <Link to="/cotacoes-massivas" className="nav-link">Cotações massivas</Link>
                                </li>
                                <li className="nav-item">
                                    <Link to="/abrangencia-transportadoras" className="nav-link">Abrang. Transportadoras</Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

            </header>  
            <div className="title-page">
                <div className="container">
                    <div className="container-header">
                        <h2>{props.title}</h2>
                        <img src={Logo} alt="Logo Estrela 10 padrão" width="250" height="80"/>
                    </div>
                </div>
            </div>
        </div>
    )
}

export default Header;