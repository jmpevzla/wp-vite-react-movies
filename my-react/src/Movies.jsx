import { useState, useEffect } from 'react';

function Movies() {
  const [movies, setMovies] = useState({
    data: {}
  })

  function fetchPostData() {
    fetch(`http://localhost/wpbase/wp-json/wp/v2/movies?per_page=100`)
      .then(response => response.json())
      .then(myJSON => {
        let objLength = myJSON.length;
        let newState = Object.assign({}, movies)
      
        for (let i = 0; i < objLength; i++) {
          let objKey = Object.values(myJSON)[i].title.rendered;
          newState.data[objKey] = {};
          
          let currentMovie = newState.data[objKey];
          currentMovie.name = Object.values(myJSON)[i].title.rendered;
          currentMovie.description = Object.values(myJSON)[i].content.rendered;
          currentMovie.featured_image = Object.values(myJSON)[i]['featured_image_url'];
          currentMovie.genre = Object.values(myJSON)[i]['movie_category'];  
        }
        
        setMovies(newState);
      });
  }

  useEffect(() => {
    fetchPostData()
  }, [])

  function populatePageAfterFetch(movie, index) {
    if (movies.data) {
      return (
        <div key={index} index={index}> 
          <h2 className="name">{movie.name}</h2> 
          <h3 className="genre">{movie.genre}</h3>
          <div className="description" dangerouslySetInnerHTML={{__html: movie.description}} />
          <img className="featured_image" src={movie.featured_image} alt={movie.name} />
        </div>
      )
    }
  }

  function renderMovies() {
    if (movies.data) {
      const moviesArray = Object.values(movies.data)
      return Object.values(moviesArray).map((movie, index) => populatePageAfterFetch(movie, index))
    }
  }

  return (
    <>
      <h1>Movies</h1>
      <div>{renderMovies()}</div>
    </>
  )
}

export default Movies
