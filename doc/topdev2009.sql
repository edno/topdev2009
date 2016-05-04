SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

-- -----------------------------------------------------
-- Table `topdev2009_Interpretes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `topdev2009_Interpretes` ;

CREATE  TABLE IF NOT EXISTS `topdev2009_Interpretes` (
  `idInterprete` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `nomInterprete` VARCHAR(100) NOT NULL ,
  `prenomInterprete` VARCHAR(50) NULL DEFAULT NULL ,
  `dateAjoutInterprete` TIMESTAMP NOT NULL ,
  PRIMARY KEY (`idInterprete`) ,
  UNIQUE INDEX `nomInterprete` (`nomInterprete` ASC, `prenomInterprete` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `topdev2009_Titres`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `topdev2009_Titres` ;

CREATE  TABLE IF NOT EXISTS `topdev2009_Titres` (
  `idTitre` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `titreTitre` VARCHAR(250) NOT NULL ,
  `anneeTitre` YEAR NULL DEFAULT NULL ,
  `dateAjoutTitre` TIMESTAMP NOT NULL ,
  PRIMARY KEY (`idTitre`) ,
  UNIQUE INDEX `titreTitre` (`titreTitre` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `topdev2009_Couples`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `topdev2009_Couples` ;

CREATE  TABLE IF NOT EXISTS `topdev2009_Couples` (
  `idCouple` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `idInterprete` INT UNSIGNED NOT NULL ,
  `idTitre` INT UNSIGNED NOT NULL ,
  `interpreteOriginal` BOOLEAN NOT NULL ,
  INDEX `fk_idInterprete` (`idInterprete` ASC) ,
  INDEX `fk_idTitre` (`idTitre` ASC) ,
  PRIMARY KEY (`idCouple`) ,
  UNIQUE INDEX `idInterpretation` (`idTitre` ASC, `idInterprete` ASC) ,
  CONSTRAINT `fk_idInterprete`
    FOREIGN KEY (`idInterprete` )
    REFERENCES `topdev2009_Interpretes` (`idInterprete` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_idTitre`
    FOREIGN KEY (`idTitre` )
    REFERENCES `topdev2009_Titres` (`idTitre` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `topdev2009_Proprietaires`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `topdev2009_Proprietaires` ;

CREATE  TABLE IF NOT EXISTS `topdev2009_Proprietaires` (
  `idProprietaire` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `pseudoProprietaire` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`idProprietaire`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `topdev2009_Fichiers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `topdev2009_Fichiers` ;

CREATE  TABLE IF NOT EXISTS `topdev2009_Fichiers` (
  `idFichier` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `nomFichier` VARCHAR(250) NOT NULL ,
  `idProprietaire` INT UNSIGNED NOT NULL ,
  `idCouple` INT UNSIGNED NOT NULL ,
  `dateAjoutFichier` TIMESTAMP NOT NULL ,
  INDEX `fr_idCouple` (`idCouple` ASC) ,
  INDEX `fk_idProprietaire` (`idProprietaire` ASC) ,
  PRIMARY KEY (`idFichier`) ,
  UNIQUE INDEX `nomFichier` (`nomFichier` ASC) ,
  CONSTRAINT `fr_idCouple`
    FOREIGN KEY (`idCouple` )
    REFERENCES `topdev2009_Couples` (`idCouple` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_idProprietaire`
    FOREIGN KEY (`idProprietaire` )
    REFERENCES `topdev2009_Proprietaires` (`idProprietaire` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Data for table `topdev2009_Proprietaires`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
INSERT INTO `topdev2009_Proprietaires` (`pseudoProprietaire`) VALUES ('Alice');
INSERT INTO `topdev2009_Proprietaires` (`pseudoProprietaire`) VALUES ('Bob');
INSERT INTO `topdev2009_Proprietaires` (`pseudoProprietaire`) VALUES ('Charles');
INSERT INTO `topdev2009_Proprietaires` (`pseudoProprietaire`) VALUES ('David');
INSERT INTO `topdev2009_Proprietaires` (`pseudoProprietaire`) VALUES ('Eric');

COMMIT;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
