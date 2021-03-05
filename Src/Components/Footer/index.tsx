import React from 'react';
import './Style.css';

export default function Footer() {
    return (
        <footer className="sticky-footer bg-dark fixed-bottom">
            <div className="container my-auto">
                <div className="copyright text-center my-auto">
                    <span id="text-copy">Copyright &copy; Cotacão E10 2021</span>
                </div>
            </div>
        </footer>
    )
}